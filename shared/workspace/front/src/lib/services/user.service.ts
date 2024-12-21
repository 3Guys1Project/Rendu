import {patch, del} from '@/lib/api';
import {toast} from 'vue-sonner';
import {User} from '@/stores/user.store.ts';

export class UserService {
    static async updateProfile(userId: number, data: Partial<User>) {
        try {
            const {success} = await patch(`utilisateurs/${userId}`, data);
            if (success) {
                toast.success('Profile updated successfully!');
                return {success};
            } else {
                toast.error('Failed to update profile. Please try again.');
                return {success: false};
            }
        } catch (error) {
            console.error('Error updating profile:', error);
            toast.error('An unexpected error occurred.');
            return {success: false};
        }
    }

    static async deleteProfile(userId: number) {
        try {
            const {success} = await del(`utilisateurs/${userId}`);
            if (success) {
                toast.success('Account deleted successfully.');
                return {success};
            } else {
                toast.error('Failed to delete account. Please try again.');
                return {success: false};
            }
        } catch (error) {
            console.error('Error deleting account:', error);
            toast.error('An unexpected error occurred.');
            return {success: false};
        }
    }
}