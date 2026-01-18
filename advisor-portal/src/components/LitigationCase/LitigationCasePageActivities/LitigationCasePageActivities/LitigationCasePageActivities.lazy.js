import React, { lazy, Suspense } from 'react';

const LazyLitigationCasePageActivities = lazy(() => import('./LitigationCasePageActivities'));

const LitigationCasePageActivities = props => (
  <Suspense fallback={null}>
    <LazyLitigationCasePageActivities {...props} />
  </Suspense>
);

export default LitigationCasePageActivities;
