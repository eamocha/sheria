import React, { lazy, Suspense } from 'react';

const LazyLitigationCasePageBasicViewDashboard = lazy(() => import('./LitigationCasePageBasicViewDashboard'));

const LitigationCasePageBasicViewDashboard = props => (
  <Suspense fallback={null}>
    <LazyLitigationCasePageBasicViewDashboard {...props} />
  </Suspense>
);

export default LitigationCasePageBasicViewDashboard;
