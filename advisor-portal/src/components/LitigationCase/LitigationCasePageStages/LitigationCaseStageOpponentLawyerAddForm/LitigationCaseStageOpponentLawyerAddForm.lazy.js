import React, { lazy, Suspense } from 'react';

const LazyLitigationCaseStageOpponentLawyerAddForm = lazy(() => import('./LitigationCaseStageOpponentLawyerAddForm'));

const LitigationCaseStageOpponentLawyerAddForm = props => (
  <Suspense fallback={null}>
    <LazyLitigationCaseStageOpponentLawyerAddForm {...props} />
  </Suspense>
);

export default LitigationCaseStageOpponentLawyerAddForm;
