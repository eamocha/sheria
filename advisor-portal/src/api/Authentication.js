import Api from "./Api";
import Axios from 'axios';

export default class Authentication extends Api {

    static login(data) {
        var formData = new FormData();

        formData.set('username', data?.email);
        formData.set('email', data?.email);
        formData.set('password', data?.password);
        formData.set('user_type', data?.user_type);
        formData.set('grant_type', this.API_GRANT_TYPE);
        formData.set('client_id', this.CLIENT.id);
        formData.set('client_secret', this.CLIENT.secret);

        return Axios.post(this.API_BASE_URL + '/login', formData, { 'Content-Type': 'multipart/form-data' });
    }

    static logOut(data) {
        var headers = this.getInitialHeaders();

        var config = {
            headers: headers
        };

        return Axios.post(this.API_BASE_URL + '/logout', null, config);
    }


    static renewAccessToken(data) {
        var formData = new FormData();

        formData.set('refresh_token', data?.refreshToken);
        formData.set('grant_type', 'refresh_token');
        formData.set('client_id', this.CLIENT.id);
        formData.set('client_secret', this.CLIENT.secret);

        return Axios.post(this.API_BASE_URL + '/renew-access-token', formData, { 'Content-Type': 'multipart/form-data' });
    }

    static requestPasswordReset(data) {
        var formData = new FormData();

        formData.set('email', data?.email);
        formData.set('user_type', data?.user_type);
        formData.set('grant_type', this.API_GRANT_TYPE);
        formData.set('client_id', this.CLIENT.id);
        formData.set('client_secret', this.CLIENT.secret);

        return Axios.post(this.API_BASE_URL + '/request-password-reset', formData, { 'Content-Type': 'multipart/form-data' });
    }

    static updatePassword(id, data) {
        var formData = new FormData();

        formData.set('password', data?.password);
        formData.set('confirm_password', data?.confirm_password);
        formData.set('token', data?.token);
        formData.set('grant_type', this.API_GRANT_TYPE);
        formData.set('client_id', this.CLIENT.id);
        formData.set('client_secret', this.CLIENT.secret);

        return Axios.post(this.API_BASE_URL + '/reset-password/' + id, formData, { 'Content-Type': 'multipart/form-data' });
    }

    static getInstanceName() {
        return Axios.get(this.API_BASE_URL + '/instance-name');
    }
}
