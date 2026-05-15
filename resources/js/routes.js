import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

const router = new Router({
	routes: [
		{
			path: '/',
			name: 'home',
			component: require('./views/Home').default
		},
    {
			path: '/clients',
			name: 'clients',
			component: require('./views/clients/index').default
		},
		{
			path: '/pos',
			name: 'pos',
			component: require('./views/pos/index').default
		},
		{
			path: '/sales',
			name: 'sales',
			component: require('./views/sales/index').default
		},
		{
			path: '/purchases',
			name: 'purchases',
			component: require('./views/purchases/index').default
		},
		{
			path: '/expenses',
			name: 'expenses',
			component: require('./views/expenses/index').default
		},
		{
			path: '/products-report',
			name: 'products-report',
			component: require('./views/products-report/index').default
		},
		{
			path: '/boxs',
			name: 'boxs',
			component: require('./views/boxs/index').default
		},
		{
			path: '/box/:id/:date',
			name: 'box',
			component: require('./views/boxs/show').default
		},
		{
			path: '/items',
			name: 'items',
			component: require('./views/items/index').default
		},
		{
			path: '/categories',
			name: 'categories',
			component: require('./views/categories/index').default
		},
		{
			path: '/providers',
			name: 'providers',
			component: require('./views/providers/index').default
		},
		{
			path: '/sellers',
			name: 'sellers',
			component: require('./views/sellers/index').default
		},
		{
			path: '/config',
			name: 'config',
			component: require('./views/config/index').default
		},
		{
			path: '*',
			component: require('./views/404').default
		}
	],
	mode: 'history',
	scrollBehavior() {
		return {x:0, y:0}
	}
});

// CHECK AUTH LARAVEL
/* router.beforeEach((to, from, next) => {
		axios.get('/check-auth').then(response => {
			if(!response.data.login){
				window.location = '/login';
        return;
				}
				else {
					next();
				}
		})
}) */

export default router;
