import React, { lazy, Suspense } from 'react';

const LazyTableHeader = lazy(() => import('./TableHeader'));

const TableHeader = props => (
    <Suspense fallback={null}>
        <LazyTableHeader {...props} />
    </Suspense>
);

export default TableHeader;
