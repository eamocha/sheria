import React, { lazy, Suspense } from 'react';

const LazyAPMainMenuBottomItems = lazy(() => import('./APMainMenuBottomItems'));

const APMainMenuBottomItems = props => (
  <Suspense fallback={null}>
    <LazyAPMainMenuBottomItems {...props} />
  </Suspense>
);

export default APMainMenuBottomItems;
