Attribute VB_Name = "get_from_scratch"
Sub read_file()

Application.ScreenUpdating = False
Application.DisplayStatusBar = False
Application.EnableEvents = False

    Dim file_ As Variant
    Dim p As Variant
    Dim p2 As Variant
    Dim p3 As Variant
    Dim p4 As Variant
    Dim p5 As String
    Dim num As Integer
    Dim line_ As String
    Dim ext As String
    ext = ".SCRATCH"
    
    Sheets("Sheet2").Select
    Cells.Select
    Selection.ClearContents
    
    Range("B:B").Select
    Selection.NumberFormat = "@"
    
    Range("C:C").Select
    Selection.NumberFormat = "#,##0.0000"
    
    Range("A1").Select
    
    p2 = InputBox("PLEASE ENTER FULL PATH FOR CHECK FILE.......")
    ''p3 = InputBox(".............PLEASE ENTER SCRATCH FILE NAME")
    p4 = InputBox("3590 OR 3592 ?????")
    p5 = p4
    ''p = p2 & "\" & p3 & ext
    
p = Dir(p2 & "\")

While p <> ""
    
    'Insert the actions to be performed on each file
    'This example will print the file name to the immediate window
    

    'Set the fileName to the next file
    p = Dir
p = p2 & p
    
        
    ''Do While file_name <> ""
        ''file_name = Dir()
On Error Resume Next

        num = FreeFile
        Open p For Input As #num
        
        If Err.Number = 76 Then
            Err.Clear
            Exit Sub
        End If
        Dim n As Integer
        n = 1
        WorksheetFunction.Trim
        Do Until EOF(1)
            Line Input #1, line_
            If p5 = "3592" Then
                If line_ Like "*3: C 3 REEL NO*" Then
                    ActiveCell.Value = WorksheetFunction.Trim(Mid(line_, WorksheetFunction.Search("REEL NO", line_) + 7, WorksheetFunction.Search("DAY", line_) - WorksheetFunction.Search("REEL NO", line_) - 7))
                    ''ActiveCell.Value = line_
                    ActiveCell.Offset(0, 1).Select
                End If
                
                If line_ Like "*2: C 2 LINE*" Then
                    ActiveCell.Value = WorksheetFunction.Trim(Mid(line_, WorksheetFunction.Search("LINE", line_) + 4, WorksheetFunction.Search("AREA", line_) - WorksheetFunction.Search("LINE", line_) - 4))
                    ActiveCell.Offset(0, 1).Select
                End If

                If line_ Like "*Transfer total*" Then
                    
                    ActiveCell.Value = WorksheetFunction.Trim(Mid(line_, WorksheetFunction.Search("Mb,", line_) + 3, WorksheetFunction.Search("Gb", line_) - WorksheetFunction.Search("Mb,", line_) - 3))
                    ActiveCell.Offset(1, -2).Select
                    
                End If
               
'            Else:
'                If line_ Like "BATCHCOPY - Copying tape*" Then
'                    ActiveCell.Value = Mid(line_, WorksheetFunction.Search("Copying tape", line_) + 12, WorksheetFunction.Search("{", line_) - WorksheetFunction.Search("Copying tape", line_) - 12)
'                    ActiveCell.Offset(0, 1).Select
'                End If
'
'                If line_ Like "BATCHCOPY - Copying tape*" Then
'                    ActiveCell.Value = Mid(line_, WorksheetFunction.Search("{", line_) + 1, WorksheetFunction.Search("}", line_) - WorksheetFunction.Search("{", line_) - 1)
'                    ''ActiveCell.Value = line_
'                    ActiveCell.Offset(0, 1).Select
'                End If
'
'
'                If line_ Like "*Transfer total*" Then
'                    ActiveCell.Value = Mid(line_, WorksheetFunction.Search("Mb,", line_) + 3, WorksheetFunction.Search("Gb", line_) - WorksheetFunction.Search("Mb,", line_) - 3) + 0
'                    ActiveCell.Offset(1, -2).Select
'                End If
            End If
            
        Loop
        Close #num
    Wend
    
Workbooks("MOBIL Integrity_Project_Weekly_Report.xlsx").Activate
Range("E:E").Select
    Selection.NumberFormat = "@"

Range("E4").Select

On Error Resume Next
If Err.Number = 1004 Then
    Err.Clear
    'ActiveCell.Offset(1, 0).Select
End If
For counter = 1 To 181
    ActiveCell.Value = WorksheetFunction.VLookup(ActiveCell.Offset(0, -2).Value, Workbooks("GET_FROM_SCRATCH_SPECIAL2.xlsm").Sheets("Sheet2").Range("$A:$C"), 2, False)
    ActiveCell.Offset(1, 0).Select
Next counter
    
End Sub
