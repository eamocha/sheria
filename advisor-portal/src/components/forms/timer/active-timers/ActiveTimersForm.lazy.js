import React, { lazy, Suspense } from 'react';

const LazyActiveTimersForm = lazy(() => import('./ActiveTimersForm'));

const ActiveTimersForm = props => (
  <Suspense fallback={null}>
    <LazyActiveTimersForm {...props} />
  </Suspense>
);

export default ActiveTimersForm;
