import React, { lazy, Suspense } from 'react';

const LazyActivity = lazy(() => import('./Activity'));

const Activity = props => (
  <Suspense fallback={null}>
    <LazyActivity {...props} />
  </Suspense>
);

export default Activity;
