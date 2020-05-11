import store from "~/store";

export default (to, from, next) => {
    if (store.getters["auth/user"].profile.description !== "ADMIN") {
        next({ name: "home" });
    } else {
        next();
    }
};
