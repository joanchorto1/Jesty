import { usePage } from '@inertiajs/inertia-vue3';

export function useAuthUser() {
    const page = usePage();
    return page.props.auth.user;
}
