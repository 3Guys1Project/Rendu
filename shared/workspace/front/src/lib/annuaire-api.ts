import {toast} from 'vue-sonner';

export async function getAnnuaireUserData(code: string) {
	try {
		const response = await fetch(import.meta.env.VITE_ANNUAIRE + '/profile/' + code);
		if (!response.ok) {
			toast.error('Erreur lors de la récupération des données');
		}
		const data = await response.json();
		toast.success('Données récupérées');
		return {
			email: data.email,
			username: data.username
		};
	} catch (e) {
		console.error(e);
		toast.error('Erreur lors de la récupération des données');
	}
	return null;
}