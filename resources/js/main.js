
// --------------------------------------------------
//  AXIOS INSTANCES
// --------------------------------------------------

const API = axios.create({
    baseURL: '/api/',
    timeout: 1000,
    headers: {
        'X-Custom-Header': 'foobar'
    },
});

const Auth = axios.create({
    baseURL: '/auth/',
    timeout: 1000,
    headers: {

    },
});



// --------------------------------------------------
//  VUE COMPONENTS
// --------------------------------------------------

const NotFoundComponent = {
    template: document.querySelector('template#not-found').innerHTML,
};

const Home = {
    template: document.querySelector('template#home').innerHTML,
};

const Login = {
    template: document.querySelector('template#login').innerHTML,
    props: {
        isLoggingIn: false,
        email: null,
        password: null,
    },
    methods: {
        logUserIn: function (e) {
            this.isLoggingIn = true;

            e.preventDefault();
            e.stopPropagation();

            console.log(this.email);
            console.log(this.password);

            this.isLoggingIn = false;
        }
    }
};



// --------------------------------------------------
//  ROUTES/ROUTER DEFINITION
// --------------------------------------------------

const Routes = [
    { path: '/',      component: Home },
    { path: '/login', component: Login },
    // { path: '/foo',   component: Foo },
    // { path: '/bar',   component: Bar },
    { path: '*',      component: NotFoundComponent },
];

const Router = new VueRouter({
    mode:   'history',
    routes: Routes,
})



// --------------------------------------------------
//  APP INSTANCE
// --------------------------------------------------

const app = new Vue({
    router: Router,
    data: {
        message: 'Hello Vue!',
    },
}).$mount('#app')
