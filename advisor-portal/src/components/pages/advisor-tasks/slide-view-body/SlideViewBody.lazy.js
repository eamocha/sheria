import React, { lazy, Suspense } from 'react';

const LazySlideViewBody = lazy(() => import('./SlideViewBody'));

const SlideViewBody = props => (
  <Suspense fallback={null}>
    <LazySlideViewBody {...props} />
  </Suspense>
);

export default SlideViewBody;
