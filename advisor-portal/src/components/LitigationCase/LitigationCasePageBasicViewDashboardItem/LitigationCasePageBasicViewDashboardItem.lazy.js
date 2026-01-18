import React, { lazy, Suspense } from 'react';

const LazyLitigationCasePageBasicViewDashboardItem = lazy(() => import('./LitigationCasePageBasicViewDashboardItem'));

const LitigationCasePageBasicViewDashboardItem = props => (
  <Suspense fallback={null}>
    <LazyLitigationCasePageBasicViewDashboardItem {...props} />
  </Suspense>
);

export default LitigationCasePageBasicViewDashboardItem;
