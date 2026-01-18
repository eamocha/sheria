import React, { lazy, Suspense } from 'react';

const LazyJudgementForm = lazy(() => import('./JudgementForm'));

const JudgementForm = props => (
  <Suspense fallback={null}>
    <LazyJudgementForm {...props} />
  </Suspense>
);

export default JudgementForm;
