import { reactive } from 'vue';
import { AuthService } from '@/lib/services/auth.service';
import { UserService } from '@/lib/services/user.service';
import {toast} from "vue-sonner";

export type User = {
    id: number;
    login: string;
    email: string;
    roles: string[];
    nom?: string;
    prenom?: string;
    telephone?: string;
};

type State = {
    user: User | undefined;
    loading: boolean;
    saving: boolean;
    initialized: boolean;
};

type Actions = {
    isLoggedIn: () => boolean;
    hasRole: (roles: string[]) => boolean;
    login: (username: string, password: string) => Promise<boolean>;
    register: (username: string, email: string, password: string, roles: string[]) => Promise<boolean>;
    logout: () => Promise<void>;
    initializeUser: () => Promise<void>;
    updateUser: (user: User) => void;
    getUser: () => User | undefined;
    updateProfile: (data: { login?: string; email?: string; plainPassword?: string }) => Promise<boolean>;
    deleteProfile: () => Promise<boolean>;
};

export const userStore = reactive<State & Actions>({
    user: undefined,
    loading: false,
    saving: false,
    initialized: false,

    isLoggedIn() {
        return !!this.user;
    },

    hasRole(requiredRoles: string[]) {
        if (!this.user) return false;
        return requiredRoles.some((role) => this.user!.roles.includes(role));
    },

    async login(username: string, password: string) {
        this.loading = true;
        const success: boolean = await AuthService.login(username, password);
        this.loading = false;
        return success;
    },

    async register(username: string, email: string, password: string, roles: string[]) {
        this.saving = true;
        const { success } = await AuthService.register(username, email, password, roles);
        this.saving = false;
        return success;
    },

    async logout() {
        this.loading = true;
        await AuthService.logout();
        this.user = undefined;
        this.loading = false;
    },

    async initializeUser() {
        this.loading = true;
        try {
            const success: boolean = await AuthService.refreshToken();
            if (!success) {
                this.user = undefined;
            }
        } catch (error) {
            console.error('Error initializing user:', error);
            this.user = undefined;
        } finally {
            this.initialized = true;
            this.loading = false;
        }
    },

    updateUser(user) {
        this.user = user;
    },

    getUser() {
        return this.user;
    },

    async updateProfile(data: {
        login?: string;
        email?: string;
        plainPassword?: string;
        nom?: string;
        prenom?: string;
        telephone?: string;
    }) {
        if (!this.user) {
            toast.error('User is not logged in.');
            return false;
        }
        this.saving = true;
        const { success } = await UserService.updateProfile(this.user.id, data);
        if (success && this.user) {
            this.updateUser({
                ...this.user,
                login: data.login || this.user.login,
                email: data.email || this.user.email,
                nom: data.nom || this.user.nom,
                prenom: data.prenom || this.user.prenom,
                telephone: data.telephone || this.user.telephone,
            });
        }
        this.saving = false;
        return success;
    },

    async deleteProfile() {
        if (!this.user) {
            toast.error('User is not logged in.');
            return false;
        }
        this.loading = true;
        const { success } = await UserService.deleteProfile(this.user.id);
        if (success) {
            this.user = undefined;
            await this.logout();
        }
        this.loading = false;
        return success;
    },
});
