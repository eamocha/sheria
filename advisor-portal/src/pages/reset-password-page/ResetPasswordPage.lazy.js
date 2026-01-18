import React, { lazy, Suspense } from 'react';

const LazyResetPasswordPage = lazy(() => import('./ResetPasswordPage'));

const ResetPasswordPage = props => (
  <Suspense fallback={null}>
    <LazyResetPasswordPage {...props} />
  </Suspense>
);

export default ResetPasswordPage;
