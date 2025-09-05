import PublicLayout from '@/layouts/PublicLayout.vue';

import Home from '@/pages/public/Home.vue';

export default [
    {
        path: "/",
        component: PublicLayout,
        children: [
            // Main Views
            { path: "", name: "Home", component: Home },
        ],
    },
];