import React, { lazy, Suspense } from 'react';

const LazyLitigationCasePageTasks = lazy(() => import('./LitigationCasePageTasks'));

const LitigationCasePageTasks = props => (
  <Suspense fallback={null}>
    <LazyLitigationCasePageTasks {...props} />
  </Suspense>
);

export default LitigationCasePageTasks;
