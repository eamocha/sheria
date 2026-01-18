import React, { lazy, Suspense } from 'react';

const LazyAttachmentItem = lazy(() => import('./AttachmentItem'));

const AttachmentItem = props => (
  <Suspense fallback={null}>
    <LazyAttachmentItem {...props} />
  </Suspense>
);

export default AttachmentItem;
