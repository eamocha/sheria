import React, { lazy, Suspense } from 'react';

const LazyLoginForm = lazy(() => import('./LoginForm'));

const LoginForm = props => (
  <Suspense fallback={null}>
    <LazyLoginForm {...props} />
  </Suspense>
);

export default LoginForm;
