<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width">
    <title>吉富德地产 -- 登录</title>
    <meta name="keywords" content="">
    <meta name="description" content="">
    <link rel="shortcut icon" href="javascript:void(0)"/>
    <link href="{{ asset('login/css/common.css')  }}?v=2" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('login/css/grid.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('login/css/logingrid.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('login/css/login.css') }}" rel="stylesheet" type="text/css">

</head>
<body style="background:#fff;">
<div class="top">
    <div class="clear"></div>
</div>

<div class="nav">
    <span>吉富德地产</span> &gt; <span>登录</span>
</div>
<div class="main zmm" id="app">
    <form action="" class="login" id="loginForm">
        <img src="{{ asset('login/img/logo.png') }}" alt="吉富德地产">
        <div class="inputele">
            <li>
                <input type="text" name="username" v-model="username" placeholder="请输入手机号码">
            </li>
            <li>
                <input type="password" name="password" v-model="password" placeholder="请输入密码">
            </li>
        </div>
        <input class="btn" type="button" @click="login" value="登 录">
    </form>

    <div class="cor">
        <p>
            吉富德地产 -- 登陆
        </p>
        <div>
            {{--<span class="qq">QQ登录</span>--}}
        </div>
    </div>
</div>
<script src="{{ asset('layer/jquery.js') }}"></script>
<script src="{{ asset('layer/layer.js') }}"></script>
<script src="{{ asset('login/js/vue.js') }}"></script>
<script src="{{ asset('layer/axios.js') }}"></script>
<script src="{{ asset('layer/jwt.js') }}"></script>
<script>

    new Vue({
        el: '#app',
        data() {
            return {
                username: null,
                password: null
            }
        },
        methods: {
            login() {
                let _this = this;
                if (_this.username && _this.password) {

                    let formData = {
                        'username': _this.username,
                        'password': _this.password
                    };

                    axios.post(`api/login`, formData).then(response => {
                        let data = response.data;
                        if (data.data.identify == 3) {
                            setToken('Bearer ' + data.meta.access_token);
                            setUser(data.data.name);
                            setUserId(data.data.id);
                            setUserPhone(_this.username);
                            window.location.href = "{{ url('index') }}";
                        } else {
                            layer.alert('非授权用户！', {icon: 0, title: '温馨提示'})
                        }
                    }).catch(error => {
                        layer.msg(error.response.data.error || '登陆失败', {icon: 5});
                        console.log(error);
                    });

                } else {
                    layer.alert('登陆信息不完整！', {icon: 5, title: '温馨提示'});
                }
            }
        }
    })
</script>
</body>
</html>