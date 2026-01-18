import React, { lazy, Suspense } from 'react';

const LazyPeople = lazy(() => import('./People'));

const People = props => (
  <Suspense fallback={null}>
    <LazyPeople {...props} />
  </Suspense>
);

export default People;
