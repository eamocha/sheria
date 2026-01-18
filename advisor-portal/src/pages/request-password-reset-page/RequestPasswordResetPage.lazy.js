import React, { lazy, Suspense } from 'react';

const LazyRequestPasswordResetPage = lazy(() => import('./RequestPasswordResetPage'));

const RequestPasswordResetPage = props => (
  <Suspense fallback={null}>
    <LazyRequestPasswordResetPage {...props} />
  </Suspense>
);

export default RequestPasswordResetPage;
