import React, { lazy, Suspense } from 'react';

const LazySlideView = lazy(() => import('./SlideView'));

const SlideView = props => (
  <Suspense fallback={null}>
    <LazySlideView {...props} />
  </Suspense>
);

export default SlideView;
