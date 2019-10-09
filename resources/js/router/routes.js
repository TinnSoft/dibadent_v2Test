const Default = () => import("~/layouts/default").then(m => m.default || m);
const Home = () => import("~/views/home").then(m => m.default || m);
const Login = () => import("~/views/auth/login").then(m => m.default || m);
const PasswordEmail = () =>
    import("~/views/auth/password/email").then(m => m.default || m);
//const PasswordReset = () => import('~/views/auth/password/reset').then(m => m.default || m)
const NotFound = () => import("~/views/errors/404").then(m => m.default || m);

export default [
    { path: "/", name: "home", component: Home },
    { path: "/login", name: "login", component: Login },
    {
        path: "/password/reset",
        name: "password.request",
        component: PasswordEmail
    },
    { path: "*", component: NotFound }
];
