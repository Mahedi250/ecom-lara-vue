import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function useCan() {
    const page = usePage();

    const permissions = computed(() => page.props.user_permissions ?? []);
    const isAdmin = computed(() => (page.props.auth?.user?.roles ?? []).includes('admin'));

    function can(permission) {
        if (!permission) return true;
        return isAdmin.value || permissions.value.includes(permission);
    }

    return { can, permissions, isAdmin };
}
