import React, { lazy, Suspense } from 'react';

const LazyRequestPasswordResetForm = lazy(() => import('./RequestPasswordResetForm'));

const RequestPasswordResetForm = props => (
  <Suspense fallback={null}>
    <LazyRequestPasswordResetForm {...props} />
  </Suspense>
);

export default RequestPasswordResetForm;
