import React, {
    lazy,
    Suspense
} from 'react';

const LazyAPRouter = lazy(() => import('./APRouter'));

const APRouter = props => (
    <Suspense fallback={null}>
        <LazyAPRouter {...props} />
    </Suspense>
);

export default APRouter;
