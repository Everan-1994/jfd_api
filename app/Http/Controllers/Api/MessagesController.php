<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Resources\MessageResource;

class MessagesController extends Controller
{
    protected $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function index(Request $request)
    {
        $user = \Auth::user();
        $id = $user['id'];
        $identify = $user['identify'];

        $message = $this->message->when(($identify == 3 || isset($request->share_id)), function ($query) use ($id) {
            return $query->whereShareId($id);
        })
            ->when(isset($request->status), function ($query) use ($request) {
                return $query->whereStatus($request->status);
            })
            ->orderBy($request->order ?: 'created_at', $request->sort ?: 'desc')
            ->paginate($request->pageSize, ['*'], 'page', $request->page ?: 1);

        return MessageResource::collection($message);
    }

    public function store(Request $request)
    {
        $ip = $request->getClientIp();

        $exists = $this->message->where([
            'ip'         => $ip,
            'article_id' => $request->article_id,
            'status'     => 0,
            'share_id'   => $request->share_id
        ])->exists();

        if ($exists) {
            return response([
                'msg' => '该项目已存在留言'
            ], 400);
        }

        $this->message->create([
            'name'       => $request->name,
            'phone'      => $request->phone,
            'share_id'   => $request->share_id,
            'article_id' => $request->article_id,
            'ip'         => $ip,
            'home_type'  => implode(',', $request->type)
        ]);

        return response([
            'msg' => '留言成功'
        ], 201);
    }

    public function changeStatus(Request $request)
    {
        $this->message->whereId($request->id)->update([
            'status'     => $request->status,
            'updated_at' => now()->toDateTimeString()
        ]);

        return response([
            'code' => 0,
            'msg'  => '更新成功'
        ]);
    }

    public function remake(Request $request)
    {
        $data = [
            'remake' => $request->remake
        ];
        if ($request->status) {
            $data['status'] = $request->status;
            $data['updated_at'] = now()->toDateTimeString();
        }
        $this->message->whereId($request->id)->update($data);

        return response([
            'code' => 0,
            'msg'  => '备注成功'
        ]);
    }

    public function del(Message $message)
    {
        $message->delete();

        return response([
            'code' => 0,
            'msg'  => '删除成功'
        ]);
    }
    
    /**
     * 最近七天内的留言
     */
    public function getWeekMessage()
    {
        $list = \DB::select("
                    select
                      DATE_FORMAT(a.created_at, '%m-%d') as days,
                      ifnull(b.count,0) as count
                    from (
                           SELECT date_sub(curdate(), interval 1 day) as created_at
                           union all
                           SELECT date_sub(curdate(), interval 2 day) as created_at
                           union all
                           SELECT date_sub(curdate(), interval 3 day) as created_at
                           union all
                           SELECT date_sub(curdate(), interval 4 day) as created_at
                           union all
                           SELECT date_sub(curdate(), interval 5 day) as created_at
                           union all
                           SELECT date_sub(curdate(), interval 6 day) as created_at
                           union all
                           SELECT date_sub(curdate(), interval 7 day) as created_at
                         ) a left join (
                                         select date(created_at) as datetime, count(*) as count
                                         from messages
                                         group by date(created_at)
                                       ) b on a.created_at = b.datetime
                                       order by days desc
                ");

        return response($list);
    }

    /**
     * 每月申报量
     */
    public function getMonthMessage(Message $message)
    {
        $list = $message->select(\DB::raw("DATE_FORMAT(created_at,'%Y-%m') as times, COUNT(*) as count"))
            ->groupBy('times')
            ->get()
            ->toArray();

        if ($list) {
            // 构造12个月 YYYY-MM
            $now = substr(now()->toDateString(), 0, 7) . '-01';
            for ($i = 0; $i <= 11; $i++) {
                $month = Carbon::parse($now)->modify('-' . $i . ' months')->toDateString();
                $ms[$i] = substr($month, 0, 7); // YYYY-MM
            }

            $ms = array_reverse($ms); // 倒序排列
            
            foreach ($ms as $c => $w) {
                foreach ($list as $k => $v) {
                    if (hash_equals($w, $v['times'])) {
                        $data[$c][$k] = $v;
                    } else {
                        $data[$c][$k] = [
                            'times' => $w,
                            'count' => 0
                        ];
                    }
                }
            }

            foreach ($data as $k => $v) {
                $sum[$k] = [];
                foreach ($v as $c => $w) {
                    array_push($sum[$k], $w['count']);
                }

                $date[$k] = [
                    'day'   => $v[0]['times'],
                    'count' => array_sum($sum[$k])
                ];
            }
        } else {
            $date = [];
        }

        return response($date);
    }
}
