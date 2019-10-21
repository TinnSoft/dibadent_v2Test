const Default = () => import("~/layouts/default").then(m => m.default || m);
const Home = () => import("~/views/home").then(m => m.default || m);
const Login = () => import("~/views/auth/login").then(m => m.default || m);
const PasswordEmail = () =>
    import("~/views/auth/password/email").then(m => m.default || m);
//const PasswordReset = () => import('~/views/auth/password/reset').then(m => m.default || m)
const NotFound = () => import("~/views/errors/404").then(m => m.default || m);

const SettingsIndex = () =>  import ('~/views/settings/index').then(m => m.default || m)
const SettingsProfile = () =>  import ('~/views/settings/profile').then(m => m.default || m)
const SettingsCompany = () =>  import ('~/views/settings/company').then(m => m.default || m)
const SettingsDoctor = () =>  import ('~/views/settings/doctor').then(m => m.default || m)
const SettingsRadiologist = () =>  import ('~/views/settings/radiologist').then(m => m.default || m)
const SettingsPatient = () =>  import ('~/views/settings/patient').then(m => m.default || m)

export default [
    { path: "/", name: "home", component: Home },
    { path: "/login", name: "login", component: Login },
    {
        path: "/password/reset",
        name: "password.request",
        component: PasswordEmail
    },
    {
        path: '/settings',   
        component: SettingsIndex,
        children: [
          { path: '', redirect: { name: 'settings.company' }},
          { path: 'profile', name: 'settings.profile', component:SettingsProfile},
          { path: 'doctor', name: 'settings.doctor', component:SettingsDoctor},
          { path: 'radiologist', name: 'settings.radiologist', component:SettingsRadiologist},
          { path: 'patient', name: 'settings.patient', component:SettingsPatient},
          { path: 'company', name: 'settings.company', component:SettingsCompany },
        ]
      },
    { path: "*", component: NotFound }
];
