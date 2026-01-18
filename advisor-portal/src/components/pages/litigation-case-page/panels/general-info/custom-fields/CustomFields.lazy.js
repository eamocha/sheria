import React, { lazy, Suspense } from 'react';

const LazyCustomFields = lazy(() => import('./CustomFields'));

const CustomFields = props => (
  <Suspense fallback={null}>
    <LazyCustomFields {...props} />
  </Suspense>
);

export default CustomFields;
