
// --------------------------------------------------
//  AXIOS INSTANCES
// --------------------------------------------------

const API = axios.create({
    baseURL: '/api',
    timeout: 1000,
    headers: {
        'X-Custom-Header': 'foobar'
    },
});

const Auth = axios.create({
    baseURL: '/auth',
    timeout: 1000,
    headers: {

    },
});




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
        if (!this.user) {
            this.$router.push({ name: 'login' });
            return;
        }

        console.log('user', this.user);
    },
});



const NotFoundComponent = {
    template: document.querySelector('template#not-found').innerHTML,
};

const Home = {
    template: document.querySelector('template#home').innerHTML,

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
                    this.$store.commit('UPDATE_USER', res.data);
                    this.$router.push({ name: 'home' });
                })
                .catch((err) => {
                    console.error(err);
                })
                .then(() => {
                    this.isLoggingIn = false;
                });
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
