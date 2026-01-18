CREATE OR ALTER VIEW dbo.contract_payments_view AS
SELECT 
    c.id AS contract_id,
    CAST(ROUND(
        COALESCE(SUM(cm.amount), 0), 
        2
    ) AS DECIMAL(19,2)) AS amount_paid_so_far,
    
    CAST(ROUND(
        CASE 
            WHEN c.value IS NULL THEN 0
            WHEN c.value = 0 THEN 0
            ELSE c.value - COALESCE(SUM(cm.amount), 0)
        END, 
        2
    ) AS DECIMAL(19,2)) AS balance_due
FROM 
    dbo.contract c
LEFT JOIN 
    dbo.contract_milestone cm 
        ON cm.contract_id = c.id
        AND cm.financial_status IN ('paid', 'partially_paid')
GROUP BY 
    c.id, c.value;
