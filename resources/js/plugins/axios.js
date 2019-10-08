import axios from "axios";
import store from "~/store";
import router from "~/router";
import { Dialog } from 'quasar';
import i18n from "~/plugins/i18n";

// Request interceptor
axios.interceptors.request.use(request => {
    const token = store.getters["auth/token"];
    if (token) {
        request.headers.common["Authorization"] = `Bearer ${token}`;
    }

    const locale = store.getters["lang/locale"];
    if (locale) {
        request.headers.common["Accept-Language"] = locale;
    }

    // request.headers['X-Socket-Id'] = Echo.socketId()

    return request;
});

// Response interceptor
axios.interceptors.response.use(
    response => response,
    error => {
        const { status } = error.response;
        try {
          console.log("plugins -> axios222");
        

            Dialog.create({
                title: "Confirm",
                message: "Would you like to turn on the wifi?",
                ok: {
                    push: true
                },
                cancel: {
                    push: true,
                    color: "negative"
                },
                persistent: false
            })
                .onOk(() => {
                    // console.log('>>>> OK')
                })
                .onCancel(() => {
                    // console.log('>>>> Cancel')
                })
                .onDismiss(() => {
                    // console.log('I am triggered on both OK and Cancel')
                });
        } catch (e) {console.log("plugins -> axios2 ", e);}

        

        if (status >= 500) {
            // swal.fire({
            //   type: 'error',
            //   title: i18n.t('error_alert_title'),
            //   text: i18n.t('error_alert_text'),
            //   reverseButtons: true,
            //   confirmButtonText: i18n.t('ok'),
            //   cancelButtonText: i18n.t('cancel')
            // })
        }

        if (status === 401 && store.getters["auth/check"]) {
            // swal.fire({
            //   type: 'warning',
            //   title: i18n.t('token_expired_alert_title'),
            //   text: i18n.t('token_expired_alert_text'),
            //   reverseButtons: true,
            //   confirmButtonText: i18n.t('ok'),
            //   cancelButtonText: i18n.t('cancel')
            // }).then(async () => {
            //   await store.dispatch('auth/logout')

            //   router.push({ name: 'login' })
            // })
            router.push({ name: "login" });
        }

        return Promise.reject(error);
    }
);
