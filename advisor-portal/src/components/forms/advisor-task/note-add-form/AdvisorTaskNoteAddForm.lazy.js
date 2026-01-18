import React, { lazy, Suspense } from 'react';

const LazyAdvisorTaskNoteAddForm = lazy(() => import('./AdvisorTaskNoteAddForm'));

const AdvisorTaskNoteAddForm = props => (
  <Suspense fallback={null}>
    <LazyAdvisorTaskNoteAddForm {...props} />
  </Suspense>
);

export default AdvisorTaskNoteAddForm;
