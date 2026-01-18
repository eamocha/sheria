import React, { lazy, Suspense } from 'react';

const LazySlideViewHeader = lazy(() => import('./SlideViewHeader'));

const SlideViewHeader = props => (
  <Suspense fallback={null}>
    <LazySlideViewHeader {...props} />
  </Suspense>
);

export default SlideViewHeader;
