import React, { lazy, Suspense } from 'react';

const LazyAttachmentItemData = lazy(() => import('./AttachmentItemData'));

const AttachmentItemData = props => (
  <Suspense fallback={null}>
    <LazyAttachmentItemData {...props} />
  </Suspense>
);

export default AttachmentItemData;
