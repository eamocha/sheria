import React, { lazy, Suspense } from 'react';

const LazyFileVersionsContainer = lazy(() => import('./FileVersionsContainer'));

const FileVersionsContainer = props => (
  <Suspense fallback={null}>
    <LazyFileVersionsContainer {...props} />
  </Suspense>
);

export default FileVersionsContainer;
