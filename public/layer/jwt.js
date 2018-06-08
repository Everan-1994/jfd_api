function setToken(token) {
    window.localStorage.setItem('jwt_token', token);
}

function getToken() {
    return window.localStorage.getItem('jwt_token');
}

function removeToken() {
    window.localStorage.removeItem('jwt_token');
}

function setUser(user) {
    window.localStorage.setItem('user', user);
}

function getUser() {
    return window.localStorage.getItem('user');
}

function removeUser() {
    window.localStorage.removeItem('user');
}

function setUserId(user_id) {
    window.localStorage.setItem('user_id', user_id);
}

function getUserId() {
    return window.localStorage.getItem('user_id');
}

function removeUserId() {
    window.localStorage.removeItem('user_id');
}

function setUserPhone(phone) {
    window.localStorage.setItem('phone', phone);
}

function getUserPhone() {
    return window.localStorage.getItem('phone');
}

function removeUserPhone() {
    window.localStorage.removeItem('phone');
}
