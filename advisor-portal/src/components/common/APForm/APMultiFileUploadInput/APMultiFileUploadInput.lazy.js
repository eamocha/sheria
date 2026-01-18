import React, { lazy, Suspense } from 'react';

const LazyAPMultiFileUploadInput = lazy(() => import('./APMultiFileUploadInput'));

const APMultiFileUploadInput = props => (
  <Suspense fallback={null}>
    <LazyAPMultiFileUploadInput {...props} />
  </Suspense>
);

export default APMultiFileUploadInput;
