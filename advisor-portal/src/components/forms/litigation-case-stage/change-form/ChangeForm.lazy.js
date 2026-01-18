import React, { lazy, Suspense } from 'react';

const LazyChangeForm = lazy(() => import('./ChangeForm'));

const ChangeForm = props => (
  <Suspense fallback={null}>
    <LazyChangeForm {...props} />
  </Suspense>
);

export default ChangeForm;
