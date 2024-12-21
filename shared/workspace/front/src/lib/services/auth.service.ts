import {post} from '@/lib/api.ts';
import {toast} from 'vue-sonner';
import {User, userStore} from '@/stores/user.store.ts';
import router from '@/lib/routes.ts';

export class AuthService {
	static async login(login: string, password: string) {
		const {success, data} = await post<{ data: User }>('auth/login', {login, password});
		if (!success) {
			toast.error('Nom d\'utilisateur ou mot de passe incorrect');
		} else {
			userStore.user = data!.data;
			toast.success('Connexion réussie');
		}
		return success;
	}

	static async register(login: string, email: string, password: string, roles: string[]) {
		const {success, data} = await post<{ data: User }>('auth/register', {
			login,
			plainPassword: password,
			email,
			roles
		}, {
			noCredentials: true
		});
		if (!success) toast.error('Erreur lors de l\'inscription');
		else toast.success('Inscription réussie, vous pouvez maintenant vous connecter');
		return {success, data};
	}

	static async refreshToken() {
		const {success, data} = await post<{ data: User }>('token/refresh', undefined, {
			refreshAllowed: false
		});
		if (success) userStore.user = data!.data;

		return success;
	}

	static async logout() {
		userStore.user = undefined;
		const {success} = await post('token/invalidate');
		if (!success) toast.error('Erreur lors de la déconnexion');
		else {
			toast.success('Déconnexion réussie');
			await router.push('/');
		}
	}
}