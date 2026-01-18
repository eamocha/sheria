import React, { lazy, Suspense } from 'react';

const LazyDropzoneContainer = lazy(() => import('./DropzoneContainer'));

const DropzoneContainer = props => (
  <Suspense fallback={null}>
    <LazyDropzoneContainer {...props} />
  </Suspense>
);

export default DropzoneContainer;
