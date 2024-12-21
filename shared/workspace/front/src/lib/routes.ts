import {createRouter, createWebHistory} from 'vue-router';
import HomePage from '@/views/Home.vue';
import LoginPage from '@/views/Login.vue';
import RegisterPage from '@/views/Register.vue';
import ParticipationsPage from '@/views/Participations.vue';
import ProfilePage from '@/views/Profile.vue';
import EventPage from '@/views/Event.vue';
import NotFoundPage from '@/views/NotFound.vue';
import MainLayout from '@/layouts/Main-Layout.vue';
import AuthLayout from '@/layouts/Auth-Layout.vue';
import {userStore} from '@/stores/user.store';

const routes = [
	{
		path: '/',
		component: MainLayout,
		children: [
			{
				path: '',
				name: 'Home',
				component: HomePage,
				meta: {requiresAuth: false}
			},
			{
				path: 'participations',
				name: 'Participations',
				component: ParticipationsPage,
				meta: {requiresAuth: true}
			},
			{
				path: 'profile',
				name: 'Profile',
				component: ProfilePage,
				meta: {requiresAuth: true}
			},
			{
				path: 'event/:id',
				name: 'Event',
				component: EventPage,
				meta: {requiresAuth: false}
				// meta: { requiresAuth: true, roles: ["ROLE_ORGA"] },
			}
		]
	},
	{
		path: '/',
		component: AuthLayout,
		children: [
			{
				path: 'login',
				name: 'Login',
				component: LoginPage,
				meta: {requiresAuth: false}
			},
			{
				path: 'register',
				name: 'Register',
				component: RegisterPage,
				meta: {requiresAuth: false}
			}
		]
	},
	{
		path: '/:pathMatch(.*)*',
		name: 'NotFound',
		component: NotFoundPage,
		meta: {requiresAuth: false}
	}
];

const router = createRouter({
	history: createWebHistory(import.meta.env.VITE_BASE_URL),
	routes
});

router.beforeEach(async (to, _from, next) => {
	if (!userStore.initialized) {
		await userStore.initializeUser();
	}

	const user = userStore.user;
	const isAuthenticated = !!user;
	const userRoles = user?.roles || [];

	if (to.meta.requiresAuth && !isAuthenticated) {
		return next({name: 'Login'});
	}

	if (Array.isArray(to.meta.roles)) {
		const hasRole = to.meta.roles.some((role: string) => userRoles.includes(role));
		if (!hasRole) return next({name: 'Home'});
	}

	next();
});


export default router;
