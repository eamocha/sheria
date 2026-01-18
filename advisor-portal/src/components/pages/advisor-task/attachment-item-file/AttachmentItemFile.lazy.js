import React, { lazy, Suspense } from 'react';

const LazyAttachmentItemFile = lazy(() => import('./AttachmentItemFile'));

const AttachmentItemFile = props => (
  <Suspense fallback={null}>
    <LazyAttachmentItemFile {...props} />
  </Suspense>
);

export default AttachmentItemFile;
