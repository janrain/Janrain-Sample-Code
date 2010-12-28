/*
 *Visual Basic Helper Class for Janrain Engage
 */

Public NotInheritable Class Rpx

        Private _APIKeyValue As String

        Private _XMLResponse As XmlDocument

        Private _CurrentRequest As HttpRequest

        Private Const _BASE_URL As String = "http://rpxnow.com/api/v2/"

        Private Const _TOKEN_VAR_NAME As String = "token"

        Private Const _FORMAT_VAR_NAME As String = "format"

        Private Const _APIKEY_VAR_NAME As String = "apiKey"

        Private Const _REQUEST_METHOD As String = "POST"

        Private Const _REQUEST_CONTENT_TYPE As String = "text/xml"

        Private Const _RPX_AUTH_METHOD_NAME As String = "auth_info"

        Public Sub New(ByVal apiKey As String, ByVal request As HttpRequest)

            _APIKeyValue = apiKey

            _CurrentRequest = request

            _XMLResponse = ProcessXMLResponse()

        End Sub

        Public ReadOnly Property ApiKeyValue As String

            Get

                Return _APIKeyValue

            End Get

        End Property

        Public ReadOnly Property BaseURL As String

            Get

                Return _BASE_URL

            End Get

        End Property

        Public ReadOnly Property XMLResponse As XmlDocument

            Get

                Return _XMLResponse

            End Get

        End Property

        Private ReadOnly Property CurrentRequest As HttpRequest

            Get

                Return _CurrentRequest

            End Get

        End Property

        Private Function GetToken() As String

            Dim ReturnValue As String

            Dim NVC As NameValueCollection = CurrentRequest.Form

            ReturnValue = NVC.Item(_TOKEN_VAR_NAME)

            Return ReturnValue

        End Function

        Private Function ProcessXMLResponse() As XmlDocument

            Dim Query As Dictionary(Of String, String) = ConfigureQueryValues()

            Dim PostData As String = String.Empty

            Dim URL As Uri = CreateRequestURL(Query, PostData)

            Dim RawXML As String = GetRawXml(URL, PostData)

            Dim WellFormedXML As XmlDocument = BuildXMLResponseDocument(RawXML)

            Return WellFormedXML

        End Function

        Private Function GetRawXml(ByVal url As Uri, ByVal postData As String) As String

            Dim ReturnValue As String = String.Empty

            Dim Rqst As HttpWebRequest = DirectCast(WebRequest.Create(url), HttpWebRequest)

            Rqst.Method = _REQUEST_METHOD

            Rqst.ContentType = _REQUEST_CONTENT_TYPE

            Dim Out As New StreamWriter(Rqst.GetRequestStream(), Encoding.ASCII)

            Out.Write(postData)

            Out.Close()

            Dim response As HttpWebResponse = DirectCast(Rqst.GetResponse(), HttpWebResponse)

            Dim ResponseStream As System.IO.Stream = response.GetResponseStream()

            Dim StrmRdr As New StreamReader(ResponseStream, Encoding.UTF8)

            ReturnValue = StrmRdr.ReadToEnd

            Return ReturnValue

        End Function

        Private Function CreateRequestURL(ByVal query As Dictionary(Of String, String), ByRef postData As String) As Uri

            Dim ReturnValue As Uri

            Dim sb As New StringBuilder()

            For Each e As KeyValuePair(Of String, String) In query

                If sb.Length = 0 Then sb.Append("?"c)

                If sb.Length > 2 Then sb.Append("&"c)

                sb.Append(System.Web.HttpUtility.UrlEncode(e.Key, Encoding.UTF8))

                sb.Append("="c)

                sb.Append(HttpUtility.UrlEncode(e.Value, Encoding.UTF8))

            Next

            postData = sb.ToString()

            Dim URIString As String = String.Concat(_BASE_URL, _RPX_AUTH_METHOD_NAME, postData)

            ReturnValue = New Uri(URIString)

            Return ReturnValue

        End Function

        Private Function ConfigureQueryValues() As Dictionary(Of String, String)

            Dim ReturnValue As New Dictionary(Of String, String)

            ReturnValue.Add(_TOKEN_VAR_NAME, GetToken())

            ReturnValue.Add(_FORMAT_VAR_NAME, "xml")

            ReturnValue.Add(_APIKEY_VAR_NAME, ApiKeyValue)

            Return ReturnValue

        End Function

        Private Function BuildXMLResponseDocument(ByVal rawXML As String) As XmlDocument

            Dim ReturnValue As XmlDocument = Nothing

            Dim Fragment As String = String.Empty

            Dim MemStream As New MemoryStream

            Dim Writer As New XmlTextWriter(MemStream, System.Text.Encoding.UTF8)

            If rawXML.StartsWith("<?xml") = True Then

                Dim EndTrimIdx As Integer = (rawXML.IndexOf(">"c) + 1)

                Fragment = rawXML.Remove(0, EndTrimIdx) 'remove existing header to construct new well formed document

            Else

                Fragment = rawXML 'no existing header found, use the rawXML value instead

            End If

            Writer.WriteStartDocument(True)

            Writer.WriteStartElement("root")

            Writer.WriteRaw(Fragment.ToCharArray)

            Writer.WriteEndElement()

            Writer.Flush()

            MemStream.Seek(0, SeekOrigin.Begin)

            Dim WellFormedXML As String

            Dim StrmReader As New StreamReader(MemStream)

            WellFormedXML = StrmReader.ReadToEnd

            ReturnValue = New XmlDocument()

            ReturnValue.LoadXml(WellFormedXML)

            Return ReturnValue

        End Function

    End Class


/*
 *     Sample codebehind page.
 *
 *Public Class Main
 *        Inherits System.Web.UI.Page
 *        Private Const _API_KEY As String = "MyAPIKeyFromRPXAccount"
 *        Private Sub Main_PreInit(ByVal sender As Object, ByVal e As System.EventArgs) Handles Me.PreInit
 *            Dim RPX As New Rpx(_API_KEY, Request)
 *            Debug.Assert(RPX.XMLResponse IsNot Nothing)
 *            'query the RPX.XMLResponse property to get the values you need.
 *        End Sub
 *        Protected Sub Page_Load(ByVal sender As Object, ByVal e As System.EventArgs) Handles Me.Load
 *        End Sub
 *    End Class
 */
