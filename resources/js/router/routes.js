function page(path) {
    return () => import(`~/views/${path}`).then(m => m.default || m);
}

export default [
    { path: "/", name: "home", component: page("home.vue") },
    { path: "/login", name: "login", component: page("auth/login.vue") },
    {
        path: "/points",
        name: "pointdoctors",
        component: page("points/index.vue")
    },
    { path: "/chat", name: "chat", component: page("admin/chat.vue") },
    {
        path: "/chat_doctor",
        name: "chat_doctor",
        component: page("doctors/chat.vue")
    },
    {
        path: "/points-redemption",
        name: "points-redemption",
        component: page("doctors/points_redemption.vue")
    },
    {
        path: "/password/reset",
        name: "password.reset",
        component: page("auth/password/reset.vue")
    },
    {
        path: "/products-redemption",
        name: "products-redemption",
        component: page("radiologist/product_redemption.vue")
    },
    {
        path: "/settings",
        component: page("settings/index.vue"),
        children: [
            { path: "", redirect: { name: "settings.profile" } },
            {
                path: "profile",
                name: "settings.profile",
                component: page("settings/profile.vue")
            },
            {
                path: "doctor",
                name: "settings.doctor",
                component: page("settings/doctor.vue")
            },
            {
                path: "radiologist",
                name: "settings.radiologist",
                component: page("settings/radiologist.vue")
            },
            {
                path: "patient",
                name: "settings.patient",
                component: page("settings/patient.vue")
            },
            {
                path: "company",
                name: "settings.company",
                component: page("settings/company.vue")
            },
            {
                path: "product",
                name: "settings.product",
                component: page("settings/product.vue")
            },
            {
                path: "password",
                name: "settings.password",
                component: page("settings/password.vue")
            }
        ]
    },
    { path: "*", component: page("errors/404.vue") }
];
