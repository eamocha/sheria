import React, { lazy, Suspense } from 'react';

const LazyAPPrioritySign = lazy(() => import('./APPrioritySign'));

const APPrioritySign = props => (
  <Suspense fallback={null}>
    <LazyAPPrioritySign {...props} />
  </Suspense>
);

export default APPrioritySign;
