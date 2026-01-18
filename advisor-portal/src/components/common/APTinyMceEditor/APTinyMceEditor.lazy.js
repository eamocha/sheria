import React, { lazy, Suspense } from 'react';

const LazyAPTinyMceEditor = lazy(() => import('./APTinyMceEditor'));

const APTinyMceEditor = props => (
  <Suspense fallback={null}>
    <LazyAPTinyMceEditor {...props} />
  </Suspense>
);

export default APTinyMceEditor;
