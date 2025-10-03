export const inventoryPalette = {
    background: 'bg-slate-950',
    gradient: 'from-amber-500 via-orange-600 to-slate-950',
    surface: 'bg-white',
    surfaceMuted: 'bg-white/80 backdrop-blur',
    border: 'border-amber-500/20',
    shadow: 'shadow-xl shadow-amber-900/20',
    text: {
        overGradient: 'text-amber-200',
        hero: 'text-white',
        heading: 'text-slate-900',
        body: 'text-slate-600',
        subtle: 'text-slate-400',
    },
    accents: {
        gold: 'bg-amber-500/10 text-amber-600',
        bronze: 'bg-yellow-500/10 text-yellow-600',
        slate: 'bg-slate-900/10 text-slate-500',
        dusk: 'bg-orange-500/10 text-orange-600',
        obsidian: 'bg-slate-950/40 text-white',
        pearl: 'bg-white/40 text-slate-600',
    },
};

export const inventoryTypography = {
    heroKicker: 'text-sm uppercase tracking-[0.3em] text-amber-200',
    heroTitle: 'text-3xl sm:text-4xl font-semibold text-white',
    heroSubtitle: 'text-sm text-amber-100/80',
    sectionTitle: 'text-xl font-semibold text-slate-900',
    sectionSubtitle: 'text-sm text-slate-600',
    overline: 'text-xs uppercase tracking-[0.3em] text-amber-300',
    statValue: 'text-3xl font-semibold text-white',
};

export const inventoryLayout = {
    heroWrapper: 'bg-gradient-to-r pb-24 rounded-b-[2.5rem] md:rounded-b-[3rem]',
    heroContainer: 'max-w-7xl mx-auto px-6 pt-10',
    kpiGrid: 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mt-10',
    bodyWrapper: 'max-w-7xl mx-auto px-6 -mt-16 pb-16 space-y-10',
    sectionGrid: 'grid grid-cols-1 xl:grid-cols-3 gap-8',
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
