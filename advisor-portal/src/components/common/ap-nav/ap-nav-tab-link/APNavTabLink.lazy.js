import React, { lazy, Suspense } from 'react';

const LazyAPNavTabLink = lazy(() => import('./APNavTabLink'));

const APNavTabLink = props => (
  <Suspense fallback={null}>
    <LazyAPNavTabLink {...props} />
  </Suspense>
);

export default APNavTabLink;
