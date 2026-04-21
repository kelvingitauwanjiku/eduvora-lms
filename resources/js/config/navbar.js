/**
 * Public main navbar — mirrors Academy-LMS header structure (Home, Courses mega, Bootcamp, More).
 * Adjust `to` / `query` when public Bootcamp/Ebook pages are added to the router.
 */
export const navbarBrand = {
    name: 'Eduvora',
    homePath: '/',
};

/** Simple top-level links (no mega menu). */
export const navbarSimpleLinks = [
    { name: 'Bootcamp', to: '/courses', title: 'Bootcamps & programs (browse courses)' },
    { name: 'Instructors', to: '/instructors' },
    { name: 'Bundles', to: '/bundles' },
    { name: 'Blog', to: '/blog' },
];

/** “More” flyout — LMS: Ebooks, Find a Tutor, Course Bundle. */
export const navbarMoreLinks = [
    { name: 'Ebooks', to: '/search', query: { q: 'ebook' }, description: 'Digital learning' },
    { name: 'Find a Tutor', to: '/instructors', query: {}, description: 'Browse instructors' },
    { name: 'Course Bundle', to: '/bundles', query: {}, description: 'Save with bundles' },
];

export const navbarCoursesMega = {
    maxCategories: 10,
    panelMinWidth: '36rem',
};
