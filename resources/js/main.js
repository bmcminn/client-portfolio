
Vue.config.devtools = true;




// --------------------------------------------------
//  AXIOS INSTANCES
// --------------------------------------------------

const Auth = axios.create({
    baseURL: '/auth',
    timeout: 1000,
    headers: {

    },
});

const API = axios.create({
    baseURL: '/api',
    timeout: 1000,
    headers: {
    },
});

// --------------------------------------------------
//  SET HELPER METHODS
// --------------------------------------------------

const Helpers = {};

Helpers.setApiAuthToken = function (token) {
    if (token) {
        API.defaults.headers.Authorization = 'bearer ' + token;
    }
};

Helpers.setApiAuthToken(localStorage.getItem('token'));




// --------------------------------------------------
//  STORE INSTANCE
// --------------------------------------------------

Vue.use(Vuex);


const vuexLocal = new window.VuexPersistence.VuexPersistence({
    key:        'vuex',                 // The key to store the state on in the storage provider.
    storage:    window.localStorage,    // or window.sessionStorage or localForage
    filter:     function(mutation) {
        console.debug('vuexLocal@mutation', mutation);
        return mutation.type == 'UPDATE_USER'
    },
});


const store = new Vuex.Store({
    state: {
        user: null,
    },

    mutators: { // synchronous

    },

    actions: { // asynchronous
        UPDATE_USER(state, payload) {
            state.user = payload;
        },
    },

    getters: {
        user(state) {
            return state.user;
        },
    },

    plugins: [
        vuexLocal.plugin,
    ],
});



// --------------------------------------------------
//  VUE COMPONENTS
// --------------------------------------------------

Vue.component('user-state', {
    computed: {
        user() { return this.$store.getters.user },
    },
    created() {
        if (!localStorage.getItem('token')) {
            this.$router.push({ name: 'login' });
            return;
        }

        console.log('user', this.user);
    },
    template: '<div style="height: 0; width: 0; display: hidden"></div>'
});



const NotFoundComponent = {
    template: document.querySelector('template#not-found').innerHTML,

    created() {
        document.title = 'Page not found...';
    }
};



const Home = {
    template: document.querySelector('template#home').innerHTML,
    created() {
        document.title = 'Welcome to Pants';
    }
};



const Dashboard = {
    template: document.querySelector('template#dashboard').innerHTML,

};



const Login = {
    template: document.querySelector('template#login').innerHTML,
    props: {
        email:      null,
        password:   null,
    },
    data() {
        return {
            isLoggingIn: false,
        }
    },
    computed() {
        return {

        }
    },
    methods: {
        logUserIn: function (e) {
            e.preventDefault();
            e.stopPropagation();

            let self = this;

            this.isLoggingIn = true;

            console.debug('email   ', this.email);
            console.debug('password', this.password);

            Auth.post('/login', {
                    params: {
                        email:      this.email,
                        password:   this.password,
                    },
                })
                .then((res) => {
                    console.debug('user:', res.data);
                    localStorage.setItem('token', res.data.token);
                    self.$store.commit('UPDATE_USER', res.data.user);
                    self.$router.push({ name: 'home' });
                    Helpers.setApiAuthToken(res.data.token);
                })
                .catch((err) => {
                    console.error(err);
                })
                .then(() => {
                    self.isLoggingIn = false;
                });
        }
    },
    created() {
        document.title = 'Client Login';
        if (localStorage.getItem('token')) {
            this.$router.push({ name: 'home' })
        }
    }
};



// --------------------------------------------------
//  ROUTES/ROUTER DEFINITION
// --------------------------------------------------

const routes = [
    { name: 'home',         path: '/',          component: Home },
    { name: 'dashboard',    path: '/dashboard', component: Dashboard },
    { name: 'login',        path: '/login',     component: Login },
    // { path: '/foo',   component: Foo },
    // { path: '/bar',   component: Bar },
    { name: '404',          path: '*',          component: NotFoundComponent },
];

const router = new VueRouter({
    mode:   'history',
    routes,
})



// --------------------------------------------------
//  APP INSTANCE
// --------------------------------------------------

const app = new Vue({
    router,
    store,
    data() {
        return {
            message: 'Hello Vue!',
        }
    },
}).$mount('#app')
