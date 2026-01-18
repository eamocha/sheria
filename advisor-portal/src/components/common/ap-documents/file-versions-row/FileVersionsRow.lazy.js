import React, { lazy, Suspense } from 'react';

const LazyFileVersionsRow = lazy(() => import('./FileVersionsRow'));

const FileVersionsRow = props => (
  <Suspense fallback={null}>
    <LazyFileVersionsRow {...props} />
  </Suspense>
);

export default FileVersionsRow;
