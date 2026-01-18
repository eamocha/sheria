import Api from 'Api'

const setRequestDefaultHeader = function () {
    var headers = Api.getInitialHeaders().headers;
    jQuery.each(headers, function (key, value) {
        axios.defaults.headers.common[key] = value;
    });
};

//if want to do something before request is sent, we can use like below but "axios.interceptors.request"
axios.interceptors.response.use(
    function (response) {
        //Any status code that lie within the range of 2xx cause this function to trigger
        //here we can modify the response
        return response;
    },
    function (error) {
        //Any status codes that falls outside the range of 2xx cause this function to trigger
        //do something in case of request error
        if (error?.response?.status == 401)
            localStorage.removeItem('api-access-token');
        return Promise.reject(error);
    }
);

export default {
    initialize: function (initCallback, failedUrl) {
        initApiAccessToken(function () {
            setRequestDefaultHeader();
            initCallback();
        }, failedUrl);
    },
};
