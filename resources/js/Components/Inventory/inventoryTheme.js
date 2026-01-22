export const inventoryPalette = {
    background: 'bg-slate-100/80',
    gradient: 'from-white via-slate-50 to-slate-100',
    surface: 'bg-white/80',
    surfaceMuted: 'bg-white/70',
    border: 'border-slate-200/70',
    shadow: 'shadow-sm',
    text: {
        overGradient: 'text-slate-400',
        hero: 'text-slate-900',
        heading: 'text-slate-900',
        body: 'text-slate-600',
        subtle: 'text-slate-400',
    },
    accents: {
        gold: 'bg-emerald-500/10 text-emerald-700',
        bronze: 'bg-sky-500/10 text-sky-700',
        slate: 'bg-slate-900/10 text-slate-500',
        dusk: 'bg-indigo-500/10 text-indigo-600',
        obsidian: 'bg-slate-950/40 text-white',
        pearl: 'bg-white/40 text-slate-600',
    },
};

export const inventoryTypography = {
    heroKicker: 'text-sm uppercase tracking-[0.3em] text-slate-400',
    heroTitle: 'text-3xl sm:text-4xl font-semibold text-slate-900',
    heroSubtitle: 'text-sm text-slate-500',
    sectionTitle: 'text-xl font-semibold text-slate-900',
    sectionSubtitle: 'text-sm text-slate-600',
    overline: 'text-xs uppercase tracking-[0.3em] text-amber-300',
    statValue: 'text-3xl font-semibold text-white',
};

export const inventoryLayout = {
    heroWrapper: 'bg-gradient-to-r pb-24 rounded-b-[2.5rem] md:rounded-b-[3rem]',
    heroContainer: 'max-w-7xl mx-auto px-6 pt-10',
    kpiGrid: 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mt-10',
    bodyWrapper: 'max-w-7xl mx-auto px-6 -mt-16 pb-16 space-y-8',
    sectionGrid: 'grid grid-cols-1 xl:grid-cols-3 gap-6',
};

export const inventoryStatusMap = {
    completed: {
        label: 'Completat',
        classes: 'bg-emerald-50 text-emerald-600 ring-emerald-500/20',
    },
    pending: {
        label: 'Pendent',
        classes: 'bg-amber-50 text-amber-600 ring-amber-500/20',
    },
    cancelled: {
        label: 'Cancel·lat',
        classes: 'bg-rose-50 text-rose-600 ring-rose-500/20',
    },
    active: {
        label: 'Actiu',
        classes: 'bg-emerald-50 text-emerald-600 ring-emerald-500/20',
    },
    inactive: {
        label: 'Inactiu',
        classes: 'bg-slate-100 text-slate-500 ring-slate-400/20',
    },
    in_stock: {
        label: 'En estoc',
        classes: 'bg-emerald-50 text-emerald-600 ring-emerald-500/20',
    },
    out_of_stock: {
        label: 'Sense estoc',
        classes: 'bg-rose-50 text-rose-600 ring-rose-500/20',
    },
};

export const resolveInventoryStatus = (status, fallback = 'default') => {
    const normalized = status?.toString().toLowerCase();
    return inventoryStatusMap[normalized] ?? {
        label: fallback,
        classes: 'bg-slate-100 text-slate-500 ring-slate-400/20',
    };
};
