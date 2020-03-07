<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>吉富德地产</title>
    <meta name="viewport"
          content="initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width">
    <link rel="stylesheet" type="text/css" href="{{ asset('home/css/common.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('home/css/style.css') }}?v=2">
</head>

<style>
    .titles {
        width: 88%;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .bodys {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 3;
        overflow: hidden;
    }

    .btn-page {
        width: 47%;
        height: 30px;
        border-radius: 3px;
        background-color: #3db1fa;
        color: #fff;
    }
    .li33 {
        width: 33% !important;
    }
</style>

<body>
<div class="xfdsctz_f">
    <ul>
        <li class="addys_f2">文章列表</li>
        <li>客户留言</li>
    </ul>
</div>
<!--占固定定位的块-->
<div class="occupy_f"></div>
<!--显示区域-->
<div class="display_f" id="app">
    <!--包的内容-->
    <div class="display_f_in">
        <!--文章列表-->
        <div id="qrcode" v-show="qrcodes" style="text-align: center; padding: 10px;"></div>
        <div class="fourcp_1">
            <!--一条开始-->
            <div class="novice_zs_f_in" v-for="(article, index) in articleList" :key="index" style="margin-bottom: 6px;">
                <!--left-->
                <div class="novice_zs_f_in_l">
                    <!--top-->
                    <div class="novice_zs_f_in_l_top" style="margin-bottom: 10px;">
                        <img src="home/title.png"/>
                        <span class="titles" v-text="article.title"></span>
                    </div>
                    <!--bottom-->
                    <div class="novice_zs_f_in_l_bottom">
                        <ul>
                            <li class="mark1_f"
                                style="width: 70% !important; margin-left: 10px; border-top: 1px dashed #EEEEEE;">
                                <div style="font-size: 12px; padding: 10px 10px 0 10px;" v-html="article.title"></div>
                            </li>
                            <li class="mark3_f"
                                style="width: 20% !important; font-size: 12px; padding: 7% 0 0 10px; border-top: 1px dashed #EEEEEE; ">
                                <div>
                                    <span v-text="article.time"></span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <!--right-->
                <a href="javascript:;" @click="shareQrcode(article.id)">
                    <div class="novice_zs_f_in_r">
                        <span style="margin-top: 85%;color: #fff;">分享</span>
                    </div>
                </a>
            </div>
            <div style="width: 100%; text-align: center;" v-show="showPage">
                <button class="btn-page" @click="more_prev" ref="more_prev">上一页</button>
                <button class="btn-page" @click="more_next" ref="more_next">下一页</button>
            </div>
        </div>
        <!--客户留言-->
        <div class="fourcp_1">
            <!--一条开始-->
            <div class="novice_zs_f_in" v-for="(message, index) in messageList" :key="index" style="margin-bottom: 6px;">
                <!--left-->
                <div class="novice_zs_f_in_l">
                    <!--top-->
                    <div class="novice_zs_f_in_l_top" style="margin-bottom: 10px;">
                        <img src="home/title.png"/>
                        <span class="titles"><i v-text="message.title"></i></span>
                    </div>
                    <!--bottom-->
                    <div class="novice_zs_f_in_l_bottom">
                        <ul>
                            <li class="mark1_f">
                                <em>姓名</em>
                                <p style="text-align: center;" v-text="message.name"></p>
                            </li>
                            <li class="mark2_f">
                                <em>联系方式</em>
                                <p style="text-align: center;" v-text="message.phone"></p>
                            </li>
                            <li class="mark3_f">
                                <em>备注</em>
                                <p class="titles" style="text-align: center;" v-if="message.remake" v-text="message.remake"></p>
                                <p style="text-align: center;" v-else> -- </p>
                            </li>
                            <li class="mark3_f" style="color: #009f95;">
                                <em v-text="message.ltime"></em>
                                <p class="titles" style="text-align: center;" v-text="message.htime" v-if="message.status == 1" ></p>
                                <p class="titles" style="text-align: center;" v-else> -- </p>
                            </li>
                        </ul>
                    </div>
                </div>
                <!--right-->
                <a href="javascript:;" v-if="message.status == 1">
                    <div class="novice_zs_f_in_r">
                        <span style="margin-top: 66%;color: #fff;">已回访</span>
                    </div>
                </a>
                <a href="javascript:;" @click="remake(message.id)"  v-else>
                    <div class="novice_zs_f_in_r" style="background: red !important;">
                        <span style="margin-top: 66%;color: #fff;">待回访</span>
                    </div>
                </a>
            </div>
            <div style="width: 100%; text-align: center;" v-show="showMPage">
                <button class="btn-page" @click="more_mprev" ref="more_mprev">上一页</button>
                <button class="btn-page" @click="more_mnext" ref="more_mnext">下一页</button>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('layer/jquery.js') }}"></script>
<script src="{{ asset('home/js/jquery.qrcode.min.js') }}"></script>
<script src="{{ asset('home/js/moment.min.js') }}"></script>
<script src="{{ asset('home/js/public.js') }}"></script>
<script src="{{ asset('layer/layer.js') }}"></script>
<script src="{{ asset('login/js/vue.js') }}"></script>
<script src="{{ asset('layer/axios.js') }}"></script>
<script src="{{ asset('layer/jwt.js') }}"></script>
<script>
    axios.defaults.headers['Authorization'] = getToken();
    moment.locale('zh-cn');
    new Vue({
        el: '#app',
        data() {
            return {
                qrcodes: false,
                total: 0,
                page: 1,
                pageSize: 5,
                showPage: true,
                articleList: [],
                m_total: 0,
                m_page: 1,
                m_pageSize: 5,
                showMPage: true,
                messageList: []
            }
        },
        watch: {
            page(value) {
                console.log('page', value);
            }
        },
        created() {
            this.getArticleList();
            this.getMessageList();
        },
        methods: {
            getArticleList() {
                let _this = this;
                let params = {
                    page: _this.page,
                    pageSize: _this.pageSize
                };
                axios.get('api/article', {params}).then(response => {
                    _this.articleList = response.data.data;
                    _this.total = response.data.meta.total;
                    if (_this.total <= (_this.page * _this.pageSize)) {
                        _this.showPage = false;
                    } else {
                        _this.showPage = true;
                    }
                    _this.articleList.forEach((v, i) => {
                        _this.articleList[i].time = moment(v.created_at, format='YYYYMMDD H:mm:ss').fromNow();
                    });
                }).catch(error => {
                    if (error.response.status == 401) {
                        layer.confirm('你的登陆信息已过期，请重新登陆。', {
                            icon: 0,
                            title: '温馨提示',
                            closeBtn: 0,
                            btn: ['朕知道了']
                        }, function(){
                            removeToken();
                            removeUser();
                            removeUserId();
                            removeUserPhone();
                            window.location.href = "{{ url('wap') }}";
                        });
                    } else {
                        layer.msg(error.response.data.error || '系统出错', {icon: 5});
                    }
                });
            },
            getMessageList() {
                let _this = this;
                let params = {
                    page: _this.m_page,
                    pageSize: _this.m_pageSize,
                };
                axios.get('api/message', {params}).then(response => {
                    _this.messageList = response.data.data;
                    _this.m_total = response.data.meta.total;
                    if (_this.m_total <= (_this.m_page * _this.m_pageSize)) {
                        _this.showMPage = false;
                    } else {
                        _this.showMPage = true;
                    }
                    _this.messageList.forEach((v, i) => {
                        _this.messageList[i].ltime = moment(v.created_at, format='YYYYMMDD H:mm:ss').fromNow();
                        _this.messageList[i].htime = moment(v.updated_at, format='YYYYMMDD H:mm:ss').fromNow();
                    });
                }).catch(error => {
                    if (error.response.status == 401) {
                        layer.confirm('你的登陆信息已过期，请重新登陆。', {
                            icon: 0,
                            title: '温馨提示',
                            closeBtn: 0,
                            btn: ['朕知道了']
                        }, function(){
                            removeToken();
                            removeUser();
                            removeUserId();
                            removeUserPhone();
                            window.location.href = "{{ url('wap') }}";
                        });
                    } else {
                        layer.msg(error.response.data.error || '系统出错', {icon: 5});
                    }
                });
            },
            more_next() {
                if (this.articleList.length == this.pageSize) {
                    this.$refs.more_prev.disabled = false;
                    this.page++;
                    this.getArticleList();
                } else {
                    this.$refs.more_next.disabled = true;
                    layer.msg('没有更多数据了。');
                }
            },
            more_prev() {
                if (this.page > 1) {
                    this.$refs.more_next.disabled = false;
                    this.page--;
                    this.getArticleList();
                } else {
                    this.$refs.more_prev.disabled = true;
                    layer.msg('已经是第一页了。');
                }
            },
            more_mnext() {
                if (this.messageList.length == this.m_pageSize) {
                    this.$refs.more_mprev.disabled = false;
                    this.m_page++;
                    this.getMessageList();
                } else {
                    this.$refs.more_mnext.disabled = true;
                    layer.msg('没有更多数据了。');
                }
            },
            more_mprev() {
                if (this.m_page > 1) {
                    this.$refs.more_mnext.disabled = false;
                    this.m_page--;
                    this.getMessageList();
                } else {
                    this.$refs.more_mprev.disabled = true;
                    layer.msg('已经是第一页了。');
                }
            },
            shareQrcode(id) {
                console.log(id);
                this.qrcodes = true;
                let share_id = getUserId();
                let phone = getUserPhone();
                let name = encodeURI(getUser());
                $('#qrcode').qrcode({
                    width: 170,
                    height: 170,
                    text: `https://jfd.lzdu.com/#/preview?id=${id}&share_id=${share_id}&phone=${phone}&name=${name}`
                });
                layer.open({
                    type: 1,
                    title: '温馨提示：可以截图分享',
                    shade: false,
                    content: $('#qrcode'), // 捕获的元素，注意：最好该指定的元素要存放在body最外层，否则可能被其它的相对元素所影响
                    cancel: function () {
                        $('#qrcode').html('');
                    }
                });
                this.qrcodes = false;
            },
            remake(id) {
                let _this = this;
                layer.prompt({title: '随便写点备注，并确认', formType: 2}, function(text, index){
                    layer.close(index);
                    axios.patch('api/message/remake', {
                        id: id,
                        remake: text,
                        status: 1
                    }).then(response => {
                        _this.getMessageList();
                        layer.msg('备注成功', {icon: 6});
                    }).catch(error => {
                         layer.msg('备注失败', {icon: 5});
                    })
                });
            }
        }
    });
</script>
</body>
</html>



